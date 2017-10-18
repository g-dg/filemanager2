<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Database
{
	const MIN_VERSION = 2000000;
	const MAX_VERSION = 2999999;

	public static $connection = null;
	protected static $db_file = '_database.sqlite3';

	protected static $lock_level = 0;

	public static function connect()
	{
		self::$db_file = Config::get('database_file');
		if (!is_file(self::$db_file) ||
				!is_readable(self::$db_file) ||
				!is_writable(self::$db_file)) {
			throw new \Exception('The database is not set up or is inaccessible');
		}

		$dsn = 'sqlite:' . self::$db_file;

		self::$connection = new \PDO($dsn);

		self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		$db_version = self::getVersionNumber();

		if ($db_version < self::MIN_VERSION || $db_version > self::MAX_VERSION) {
			throw new \Exception('Incompatable database version');
		}

		self::query('PRAGMA foreign_keys = ON;');
	}

	public static function setLock($state)
	{
		if ($state) {
			if (self::$lock_level == 0) {
				//if (!self::$connection->inTransaction()) {
					self::$connection->beginTransaction();
				//}
			}
			self::$lock_level++;
		} else {
			if (self::$lock_level == 1) {
				//if (self::$connection->inTransaction()) {
					self::$connection->commit();
				//}
			}
			self::$lock_level--;
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

	public static function getVersionNumber()
	{
		return self::query('PRAGMA user_version;')[0][0];
	}

	public static function getVersionString()
	{
		$version_number = self::getVersionNumber();
		$major = floor($version_number / 1000000);
		$minor = floor($version_number / 1000) % 1000;
		$revision = $version_number % 1000;
		return $major . '.' . $minor . '.' . $revision;
	}
}

Loader::registerInit(__NAMESPACE__ . '\Database::connect');
