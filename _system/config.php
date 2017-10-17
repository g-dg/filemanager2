<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Config
{
	protected static $config = [
		'base_uri' => null,
		'index_page' => 'index.php',
		'database_file' => '_database.sqlite3',
		'log_file' => '_log.txt',
		'log_level' => 6
	];

	public static function load()
	{
		require_once('_config.php');
		foreach ($config as $option => $value) {
			self::$config[ $option ] = $value;
		}
		unset($config);
	}

	public static function get($key)
	{
		if (isset(self::$config[ $key ])) {
			return self::$config[ $key ];
		} else {
			throw new Exception('Unavailable config key: "' . $key .'"');
		}
	}
}
