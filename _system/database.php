<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Database
{
	public static $connection = null;
	protected static $lock_state = false;

	public static function connect()
	{
		
	}

	public static function setLock($state)
	{
		
	}
	public static function getLock()
	{
		return self::$lock_state;
	}

	public static function query($sql, $params)
	{
		
	}

	public static function getVersion()
	{

	}
}

Loader::registerInit(__NAMESPACE__ . '\Database::connect');
