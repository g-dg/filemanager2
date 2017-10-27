<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Router
{
	const DEFAULT_PAGE = 'index';

	protected static $registered_pages = [];
	protected static $registered_error_pages = [];

	public static function registerPage($name, $callback)
	{
		if (!isset(self::$registered_pages[$name])) {
			self::$registered_pages[$name] = $callback;
		} else {
			throw new \Exception('The page "' . $name . '" has already been registered');
		}
	}

	public static function registerErrorPage($http_error, $callback)
	{
		self::$registered_error_pages[$http_error] = $callback;
	}

	public static function execCurrentPage()
	{
		$current_page = self::getCurrentPage();
		if (isset(self::$registered_pages[$current_page])) {
			call_user_func(self::$registered_pages[$current_page], self::getCurrentParameters());
		} else {
			self::execErrorPage(404);
		}
	}

	public static function execErrorPage($http_error)
	{
		Log::notice('HTTP Error ' . $http_error);
		http_response_code($http_error);
		if (isset(self::$registered_error_pages[$http_error])) {
			call_user_func(self::$registered_error_pages[$http_error], $http_error, self::getCurrentPageAndParameters());
		}
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
		return GlobalSettings::get('_router_default_page', self::DEFAULT_PAGE);
	}

	public static function getCurrentParameters()
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
		$page = self::getCurrentPage();
		$params = self::getCurrentParameters();
		if ($params === '') {
			return $page;
		} else {
			return $page . '/' . $params;
		}
	}

	// $params is a key-value array of get parameters
	// note: if a key is numeric, the value is interpreted as a single parameter
	public static function getHttpReadyUri($full_path, $params = [])
	{
		$raw_path_array = explode('/', $full_path);
		$encoded_path_array = [];
		foreach ($raw_path_array as $pathpart) {
			if ($pathpart !== '') {
				// urlencode changes spaces into plus's. That only works with get request strings, not paths.
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

		// generate the GET request string
		$encoded_param_string = '';
		if (count($params) > 0) {
			$encoded_param_array = [];
			foreach ($params as $param => $value) {
				// deal with single parameters
				if (is_int($param)) {
					$encoded_param_array[] = urlencode($value);
				} else {
					$encoded_param_array[] = urlencode($param) . '=' . urlencode($value);
				}
			}
			$encoded_param_string = '?' . implode('&', $encoded_param_array);
		}
		return $base_uri . $encoded_path . $encoded_param_string;
	}

	// $params is a key-value array of get parameters
	public static function redirect($full_path, $params = [])
	{
		$http_path = self::getHttpReadyUri($full_path, $params);
		header('Location: ' . $http_path);
		Log::info('Redirecting to "' . $http_path . '"');
		exit();
	}

	public static function getApplicationRelativeHttpPath($relative_path)
	{
		$base_directory = pathinfo($_SERVER['SCRIPT_NAME'])['dirname'];
		$raw_path_array = explode('/', $relative_path);
		$encoded_path_array = [];
		foreach ($raw_path_array as $pathpart) {
			if ($pathpart !== '') {
				// urlencode changes spaces into plus's. That only works with get request strings, not paths.
				$encoded_path_array[] = str_replace('+', '%20', urlencode($pathpart));
			}
		}
		$encoded_path = $base_directory . '/' . implode('/', $encoded_path_array);
	}
}
