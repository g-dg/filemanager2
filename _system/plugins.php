<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Plugins
{
	protected static $registered_inits = [];

	public static function loadAll()
	{
		if (is_dir('_plugins') && $plugin_directory_handle = opendir('_plugins')) {
			while (($plugin = readdir($plugin_directory_handle)) !== false) {
				if (substr($plugin, 0, 1) !== '.' &&
						is_dir('_plugins/' . $plugin) &&
						is_readable('_plugins/' . $plugin) &&
						is_file('_plugins/' . $plugin . '/' . $plugin . '.php') &&
						is_readable('_plugins/' . $plugin . '/' . $plugin . '.php')) {
					require_once('_plugins/' . $plugin . '/' . $plugin . '.php');
				}
			}
		} else {
			throw new \Exception('Could not read the plugins directory!');
		}

		// execute the registered inits
		foreach (self::$registered_inits as $init_function) {
			call_user_func($init_function);
		}
	}

	public static function exists($plugin)
	{
		return (is_dir('_plugins') &&
				is_readable('_plugins') &&
				is_dir('_plugins/' . $plugin) &&
				is_readable('_plugins/' . $plugin) &&
				is_file('_plugins/' . $plugin . '/' . $plugin . '.php') &&
				is_readable('_plugins/' . $plugin . '/' . $plugin . '.php'));
	}

	public static function registerInit($init_function)
	{
		array_push(self::$registered_inits, $init_function);
	}
}
