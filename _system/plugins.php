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
		if (is_dir('_plugins') && $dh = opendir('_plugins')) {
			while (($file = readdir($dh)) !== false) {
				// check if ends in '.php' and doesn't start with a dot
				if (substr($file, -1, 4) === '.php' && substr($file, 0, 1) !== '.') {
					require_once('_plugins/' . $file);
				}
			}
		} else {
			throw new Exception('Could not read the plugins directory!');
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
}
