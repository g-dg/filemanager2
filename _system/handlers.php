<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Handler
{
	protected static $registered_handlers = [];

	public static function register($handler_name, $callback)
	{
		if (!isset(self::$registered_handlers[$handler_name])) {
			self::$registered_handlers[$handler_name] = [$callback];
		} else {
			array_push(self::$registered_handlers[$handler_name], $callback);
		}
	}

	public static function invoke($handler_name, $arguments, $last_only = false)
	{
		if (is_array(self::$registered_handlers[$handler_name])) {
			if ($last_only) {
				call_user_func_array(self::$registered_handlers[$handler_name][count(self::$registered_handlers[$handler_name]) - 1], $arguments);
			} else {
				foreach($self::$registered_handlers[$handler_name] as $handler_function) {
					call_user_func_array($handler_function, $arguments);
				}
			}
		}
	}

}
