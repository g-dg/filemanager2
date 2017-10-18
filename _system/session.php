<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Session
{
	const SESSION_NAME = 'SESSID';
	const SESSION_ID_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const SESSION_ID_LENGTH = 32;
	const GARBAGE_COLLECT_PROBABLILITY = 1000;

	protected static $session_id = null;

	// passing $session_id changes the current session id to that session id
	public static function start($session_id = null)
	{
		// 1/1000 chance of garbage collection
		if (mt_rand(1, self::GARBAGE_COLLECT_PROBABLILITY) == 1) {
			self::garbageCollect();
		}

		if (isset($session_id)) {
			self::$session_id = $session_id;
		} else {
			if (isset($_COOKIE[self::SESSION_NAME])) {
				self::$session_id = $_COOKIE[self::SESSION_NAME];
			} else {
				if (isset($_POST[self::SESSION_NAME])) {
					self::$session_id = $_POST[self::SESSION_NAME];
				} else {
					if (isset($_GET[self::SESSION_NAME])) {
						self::$session_id = $_GET[self::SESSION_NAME];
					} else {
						self::$session_id = self::generateSessionID();
					}
				}
			}
		}

		// generate a new id if the session id doesn't exist
		Database::setLock(true);
		if (Database::query('SELECT COUNT() from "sessions" WHERE "session_id" = ?;', [self::$session_id])[0][0] == 0) {
			self::$session_id = self::generateSessionID();
			// create the session record
			Database::query('INSERT INTO "sessions" ("session_id") VALUES (?);', [self::$session_id]);
			Session::set('__remote_addr', $_SERVER['REMOTE_ADDR']);
		} else {
			// check if the timestamp is too old
			if (Database::query('SELECT "timestamp" from "sessions" WHERE "session_id" = ?;', [self::$session_id])[0][0] >= (time() - Config::get('session_max_age'))) {
				// update timestamp
				Database::query('UPDATE "sessions" SET "timestamp" = (STRFTIME(\'%s\', \'now\')) WHERE "session_id" = ?;', [self::$session_id]);
			} else {
				self::$session_id = self::generateSessionID();
				// create the session record
				Database::query('INSERT INTO "sessions" ("session_id") VALUES (?);', [self::$session_id]);
			}
		}
		// generate new session id if ip address mismatch
		if ($_SERVER['REMOTE_ADDR'] !== Session::get('__remote_addr')) {
			setcookie(Session::SESSION_NAME, self::generateSessionID(), time() - 86400, '/', null, false, true);
			Database::setLock(false);
			Router::redirect('/');
			exit();
		} else {
			setcookie(self::SESSION_NAME, self::$session_id, 0, '/', null, false, true);
		}
		Database::setLock(false);
	}

	public static function getSessionID()
	{
		return self::$session_id;
	}

	public static function set($key, $value)
	{
		Database::query('INSERT INTO "session_data" ("session_id", "key", "value") VALUES (?, ?, ?);', [self::$session_id, $key, $value]);
	}

	public static function get($key, $default = null)
	{
		//Database::setLock(true);
		if (self::isset($key)) {
			$value = Database::query('SELECT "value" FROM "session_data" WHERE "session_id" = ? AND "key" = ?;', [self::$session_id, $key])[0][0];
		} else {
			$value = $default;
		}
		//Database::setLock(false);
		return $value;
	}

	public static function delete($key)
	{
		Database::query('DELETE FROM "session_data" WHERE "session_id" = ? AND "key" = ?;', [self::$session_id, $key]);
	}

	public static function isset($key)
	{
		return Database::query('SELECT COUNT() from "session_data" WHERE "session_id" = ? AND "key" = ?;', [self::$session_id, $key])[0][0] > 0;
	}

	public static function garbageCollect()
	{
		Database::query('DELETE FROM "sessions" WHERE "timestamp" < ?;', [time() - Config::get('session_max_age')]);
	}

	protected static function generateSessionID()
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
		for ($i = 0; $i < 32; $i++) {
			//TODO: make this more secure
			$string .= substr(self::SESSION_ID_CHARS, mt_rand(0, strlen(self::SESSION_ID_CHARS) - 1), 1);
		}
		return $string;
	}
}
