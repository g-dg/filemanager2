<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Database
{
	protected static $min_version = 2000000;
	protected static $max_version = 2999999;

	public static $connection = null;
	protected static $db_file = '_database.sqlite3';

	protected static $lock_level = 0;

	public static $cache_enabled = false;
	protected static $query_cache = [];

	public static function connect()
	{
		if (is_null(self::$connection)) {
			self::$db_file = Config::get('database_file');
			if (!is_file(self::$db_file) ||
					!is_readable(self::$db_file) ||
					!is_writable(self::$db_file)) {
				throw new \Exception('The database is not set up or is inaccessible');
			}

			$dsn = 'sqlite:' . self::$db_file;

			self::$connection = new \PDO($dsn);

			self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			self::$connection->setAttribute(\PDO::ATTR_TIMEOUT, 60);

			self::query('PRAGMA journal_mode=WAL;', [], false);
			self::query('PRAGMA synchronous=NORMAL;', [], false);

			$db_version = self::getVersionNumber();

			if ($db_version < self::$min_version || $db_version > self::$max_version) {
				throw new \Exception('Incompatable database version');
			}

			self::query('PRAGMA foreign_keys = ON;', [], false);

			self::$cache_enabled = GlobalSettings::get('_database.cache_enabled', self::$cache_enabled);
		}
	}

	public static function lock()
	{
		self::connect();
		if (self::$lock_level <= 0) {
			//if (!self::$connection->inTransaction()) {
				self::$connection->beginTransaction();
			//}
		}
		self::$lock_level++;
	}
	public static function unlock()
	{
		if (self::$lock_level == 1) {
			//if (self::$connection->inTransaction()) {
				self::$connection->commit();
			//}
		}
		self::$lock_level--;
	}

	public static function query($sql, $params = [], $enable_cache = true)
	{
		self::connect();

		if (self::$cache_enabled && $enable_cache) {
			$cache_key = $sql . json_encode($params);
			if (isset(self::$query_cache[$cache_key])) {
				return self::$query_cache[$cache_key];
			}
		}

		$done_retrying = false;
		$start_time = time();
		while (!$done_retrying) {
			try {
				$stmt = self::$connection->prepare($sql);
				$stmt->execute($params);
				$done_retrying = true;
			} catch (\PDOException $e) {
				// keep retrying if locked
				if (substr_count($e->getMessage(), 'database is locked') == 0) {
					throw $e;
				} else {
					if (time() - $start_time > 60) {
						throw $e;
					}
					usleep(1000);
				}
			}
		}
		$result = $stmt->fetchAll();

		if (self::$cache_enabled && $enable_cache) {
			$cache_key = $sql . json_encode($params);
			self::$query_cache[$cache_key] = $result;
		}

		return $result;
	}

	protected static function getVersionNumber()
	{
		self::connect();
		return self::query('PRAGMA user_version;', [], false)[0][0];
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
