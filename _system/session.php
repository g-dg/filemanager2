<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Session
{
	const SESSION_NAME = 'SESSID';
	const SESSION_ID_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const SESSION_ID_LENGTH = 32;
	const GARBAGE_COLLECT_PROBABLILITY = 1000;
	const SESSION_MAX_AGE = 86400;

	protected static $session_id = null;
	protected static $session_name = self::SESSION_NAME;

	// passing $session_id changes the current session id to that session id
	public static function start($session_id = null)
	{
		if (is_null(self::$session_id)) {
			if (mt_rand(1, GlobalSettings::get('_session_garbage_collect_inverse_probability', self::GARBAGE_COLLECT_PROBABLILITY)) == 1) {
				self::garbageCollect();
			}

			self::$session_name = GlobalSettings::get('_session_name', self::SESSION_NAME);

			if (isset($session_id)) {
				self::$session_id = $session_id;
			} else {
				if (isset($_COOKIE[self::$session_name])) {
					self::$session_id = $_COOKIE[self::$session_name];
				} else {
					self::$session_id = self::generateSessionId();
				}
			}

			Database::lock();

			// generate a new id if the session id doesn't exist
			// or if the timestamp is too old
			if (Database::query('SELECT COUNT() from "sessions" WHERE "session_id" = ? AND "timestamp" >= ?;', [self::$session_id, (time() - GlobalSettings::get('_session_max_age', self::SESSION_MAX_AGE))])[0][0] == 0) {
				// create the session record
				self::$session_id = self::generateSessionId();
				Database::query('INSERT INTO "sessions" ("session_id") VALUES (?);', [self::$session_id]);
				Session::set('_session_remote_addr', $_SERVER['REMOTE_ADDR']);
			} else {
				// update timestamp
				Database::query('UPDATE "sessions" SET "timestamp" = (STRFTIME(\'%s\', \'now\')) WHERE "session_id" = ?;', [self::$session_id], false);
			}

			// generate new session id if ip address doesn't match
			if ($_SERVER['REMOTE_ADDR'] !== Session::get('_session_remote_addr')) {
				self::$session_id = self::generateSessionId();
				Database::query('INSERT INTO "sessions" ("session_id") VALUES (?);', [self::$session_id]);
				Session::set('_session_remote_addr', $_SERVER['REMOTE_ADDR']);
			}

			Database::unlock();

			setcookie(self::$session_name, self::$session_id, 0, '/');
		}
	}

	public static function getSessionId()
	{
		self::start();
		return self::$session_id;
	}

	public static function set($key, $value)
	{
		self::start();
		Database::query('INSERT INTO "session_data" ("session_id", "key", "value") VALUES (?, ?, ?);', [self::$session_id, $key, serialize($value)]);
	}

	public static function get($key, $default = null)
	{
		self::start();
		$query_result = Database::query('SELECT "value" FROM "session_data" WHERE "session_id" = ? AND "key" = ?;', [self::$session_id, $key]);
		if (isset($query_result[0])) {
			$value = unserialize($query_result[0][0]);
		} else {
			$value = $default;
		}
		return $value;
	}

	public static function unset($key)
	{
		self::start();
		Database::query('DELETE FROM "session_data" WHERE "session_id" = ? AND "key" = ?;', [self::$session_id, $key]);
	}

	public static function isset($key)
	{
		self::start();
		return Database::query('SELECT COUNT() from "session_data" WHERE "session_id" = ? AND "key" = ?;', [self::$session_id, $key])[0][0] > 0;
	}

	public static function garbageCollect()
	{
		Database::query('DELETE FROM "sessions" WHERE "timestamp" < ?;', [time() - (GlobalSettings::get('_session_max_age', self::SESSION_MAX_AGE) + 3600)]);
	}

	protected static function generateSessionId()
	{
		if (function_exists('random_int')) {
			try {
				$string = '';
				for ($i = 0; $i < self::SESSION_ID_LENGTH; $i++) {
					$string .= substr(self::SESSION_ID_CHARS, random_int(0, strlen(self::SESSION_ID_CHARS) - 1), 1);
				}
				return $string;
			} catch (\Exception $e) {}
		}
		$string = '';
		for ($i = 0; $i < self::SESSION_ID_LENGTH; $i++) {
			//TODO: make this more secure
			$string .= substr(self::SESSION_ID_CHARS, mt_rand(0, strlen(self::SESSION_ID_CHARS) - 1), 1);
		}
		return $string;
	}

	public static function unsetSession($destroy_session = false)
	{
		self::start();
		if ($destroy_session) {
			Database::query('DELETE FROM "sessions" WHERE "session_id" = ?;', [self::$session_id]);
		}
		setcookie(self::$session_name, self::generateSessionId(), time() - 86400, '/');
		exit();
	}

	public static function lock()
	{
		Database::lock();
	}
	public static function unlock()
	{
		Database::unlock();
	}
}
