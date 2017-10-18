<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Database
{
	public static $connection = null;
	protected static $db_file = '_database.sqlite3';

	public static function connect()
	{
		self::$db_file = Config::get('database_file');
		if (!is_file(self::$db_file) ||
				!is_readable(self::$db_file) ||
				!is_writable(self::$db_file)) {
			throw new \Exception('The database is not set up or is inaccessible!');
		}

		$dsn = 'sqlite:' . self::$db_file;

		self::$connection = new \PDO($dsn);

		self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	public static function setLock($state)
	{
		if ($state) {
			if (!self::$connection->inTransaction()) {
				self::$connection->beginTransaction();
			}
		} else {
			if (self::$connection->inTransaction()) {
				self::$connection->commit();
			}
		}
	}
	public static function getLock()
	{
		return self::$connection->inTransaction();
	}

	public static function query($sql, $params = [])
	{
		$stmt = self::$connection->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	public static function getVersion()
	{
		$version_number = self::query('PRAGMA user_version;')[0][0];
		$major = floor($version_number / 1000000);
		$minor = floor($version_number / 1000) % 1000;
		$revision = $version_number % 1000;
		return $major . '.' . $minor . '.' . $revision;
	}
}

Loader::registerInit(__NAMESPACE__ . '\Database::connect');
