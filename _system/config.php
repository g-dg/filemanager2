<?php
namespace GarnetDG\FileManager;

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
		'log_level' => 6,
		'default_page' => 'index',
		'session_max_age' => 86400
	];

	protected static $loaded = false;

	public static function load()
	{
		if (!self::$loaded) {
			self::$loaded = true;
			require_once('_config.php');
			foreach ($config as $option => $value) {
				self::$config[ $option ] = $value;
			}
			unset($config);
		}
	}

	public static function get($key)
	{
		self::load();
		if (isset(self::$config[ $key ])) {
			return self::$config[ $key ];
		} else {
			return null;
		}
	}
}
