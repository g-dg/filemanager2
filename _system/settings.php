<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class GlobalSettings
{
	public static function get($key, $default = null, $force = false)
	{
		// don't authenticate for this
		Database::lock();
		if (self::isset($key)) {
			$value = Database::query('SELECT "value" FROM "global_settings" WHERE "key" = ?;', [$key])[0][0];
		} else {
			$value = $default;
		}
		Database::unlock();
		return $value;
	}

	public static function set($key, $value, $force = false)
	{
		if (Auth::getCurrentUserType() >= Auth::USER_TYPE_ADMIN || $force) {
			Database::query('INSERT INTO "global_settings" ("key", "value") VALUES (?, ?);', [$key, $value]);
		}
	}

	public static function isset($key, $force = false)
	{
		return Database::query('SELECT COUNT() from "global_settings" WHERE "key" = ?;', [$key])[0][0] > 0;
	}

	public static function unset($key, $force = false)
	{
		if (Auth::getCurrentUserType() >= Auth::USER_TYPE_ADMIN || $force) {
			Database::query('DELETE FROM "global_settings" WHERE "key" = ?;', [$key]);
		}
	}
}

class UserSettings
{
	public static function get($key, $default, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}
		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType >= Auth::USER_TYPE_ADMIN || $force) {
			Database::lock();
				if (self::isset($key, $user)) {
					$value = Database::query('SELECT "value" FROM "user_settings" WHERE "key" = ? AND "user_id" = ?;', [$key, $user])[0][0];
				} else {
					$value = $default;
				}
			Database::unlock();
			return $value;
		}
	}

	public static function set($key, $value, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}
		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType >= Auth::USER_TYPE_ADMIN || $force) {
			Database::query('INSERT INTO "user_settings" ("key", "user_id", "value") VALUES (?, ?, ?);', [$key, $user, $value]);
		}
	}

	public static function isset($key, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}
		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType >= Auth::USER_TYPE_ADMIN || $force) {
			return Database::query('SELECT COUNT() from "user_settings" WHERE "key" = ? AND "user_id" = ?;', [$key, $user])[0][0] > 0;
		}
	}

	public static function unset($key, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}
		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType >= Auth::USER_TYPE_ADMIN || $force) {
			Database::query('DELETE FROM "user_settings" WHERE "key" = ? AND "user_id" = ?;', [$key, $user]);
		}
	}
}
