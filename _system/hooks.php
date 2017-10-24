<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Hooks
{
	protected static $registered_hooks = [];

	public static function register($hook_name, $callback)
	{
		if (!self::isRegistered($hook_name)) {
			self::$registered_hooks[$hook_name] = [$callback];
		} else {
			self::$registered_hooks[$hook_name][] = $callback;
		}
	}

	public static function invoke($hook_name, $arguments, $last_only = false)
	{
		if (is_array(self::$registered_hooks[$hook_name])) {
			if ($last_only) {
				call_user_func_array(self::$registered_hooks[$hook_name][count(self::$registered_hooks[$hook_name]) - 1], $arguments);
			} else {
				foreach(self::$registered_hooks[$hook_name] as $hook_function) {
					call_user_func_array($hook_function, $arguments);
				}
			}
		}
	}

	public static function isRegistered($hook_name)
	{
		return isset(self::$registered_hooks[$hook_name]);
	}

}
