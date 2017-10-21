<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Loader
{
	protected static $registered_inits = [];

	public static function loadAll()
	{
		if (is_dir('_system') && $dh = opendir('_system')) {
			while (($file = readdir($dh)) !== false) {
				// check if ends in '.php' and doesn't start with a dot
				if (substr($file, -4, 4) === '.php' && substr($file, 0, 1) !== '.') {
					require_once('_system/' . $file);
				}
			}
		} else {
			throw new \Exception('Could not read the base system directory');
		}

		// execute the registered inits
		foreach (self::$registered_inits as $init_function) {
			call_user_func($init_function);
		}
	}

	public static function registerInit($init_function)
	{
		self::$registered_inits[] = $init_function;
	}

}
