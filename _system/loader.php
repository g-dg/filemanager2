<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Loader
{
	protected static $registered_inits = [];
	protected static $registered_handlers = [];

	public static function loadAll()
	{
		if (is_dir('_system') && $dh = opendir('_system')) {
			while (($file = readdir($dh)) !== false) {
				// check if ends in '.php' and doesn't start with a dot
				if (substr($file, -1, 4) === '.php' && substr($file, 0, 1) !== '.') {
					require_once('_system/' . $file);
				}
			}
		} else {
			throw new Exception('Could not read the base system directory!');
		}

		// execute the registered inits
		foreach (self::$registered_inits as $init_function) {
			call_user_func($init_function);
		}
	}

	public static function registerInit($init_function)
	{
		array_push(self::$registered_inits, $init_function);
	}

	public static function registerHandler($handler_name, $callback)
	{
		if (!isset(self::$registered_handlers[$handler_name])) {
			self::$registered_handlers[$handler_name] = [$callback];
		} else {
			array_push(self::$registered_handlers[$handler_name], $callback);
		}
	}

	public static function invokeHandlers($handler_name, $arguments, $last_only = false)
	{
		if ($last_only) {
			call_user_func_array(self::$registered_handlers[$handler_name][count(self::$registered_handlers[$handler_name]) - 1], $arguments);
		} else {
			foreach($self::$registered_handlers[$handler_name] as $handler_function) {
				call_user_func_array($handler_function, $arguments);
			}
		}
	}

}
