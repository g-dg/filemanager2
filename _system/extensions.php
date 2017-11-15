<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Extensions
{
	protected static $registered_inits = [];

	public static function loadAll()
	{
		if (is_dir('_extensions') && $extension_directory_handle = opendir('_extensions')) {
			while (($extension = readdir($extension_directory_handle)) !== false) {
				if (
					substr($extension, 0, 1) !== '.' &&
					substr($extension, 0, 1) !== '_' &&
					is_dir('_extensions/' . $extension) &&
					is_readable('_extensions/' . $extension) &&
					is_file('_extensions/' . $extension . '/' . $extension . '.php') &&
					is_readable('_extensions/' . $extension . '/' . $extension . '.php')
				) {
					require_once('_extensions/' . $extension . '/' . $extension . '.php');
				}
			}
		} else {
			Log::critical('Could not read the extensions directory');
			throw new \Exception('Could not read the extensions directory');
		}

		// execute the registered inits
		foreach (self::$registered_inits as $init_function) {
			call_user_func($init_function);
		}
	}

	public static function exists($extension)
	{
		return (
			is_dir('_extensions') &&
			is_readable('_extensions') &&
			is_dir('_extensions/' . $extension) &&
			is_readable('_extensions/' . $extension) &&
			is_file('_extensions/' . $extension . '/' . $extension . '.php') &&
			is_readable('_extensions/' . $extension . '/' . $extension . '.php')
		);
	}

	public static function registerInit($init_function)
	{
		self::$registered_inits[] = $init_function;
	}
}
