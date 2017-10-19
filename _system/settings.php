<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class GlobalSettings
{
	public static function get($key, $default = null)
	{
		// don't authenticate for this
		Database::setLock(true);
		if (self::isset($key)) {
			$value = Database::query('SELECT "value" FROM "global_settings" WHERE "key" = ?;', [$key])[0][0];
		} else {
			$value = $default;
		}
		Database::setLock(false);
		return $value;
	}

	public static function set($key, $value)
	{
		if (Auth::getCurrentUserType() >= Auth::USER_TYPE_ADMIN) {
			Database::query('INSERT INTO "global_settings" ("key", "value") VALUES (?, ?);', [$key, $value]);
		}
	}

	public static function isset($key)
	{
		return Database::query('SELECT COUNT() from "global_settings" WHERE "key" = ?;', [$key])[0][0] > 0;
	}

	public static function unset($key)
	{
		if (Auth::getCurrentUserType() >= Auth::USER_TYPE_ADMIN) {
			Database::query('DELETE FROM "global_settings" WHERE "key" = ?;', [$key]);
		}
	}
}

class UserSettings
{
	public static function get($key, $default, $user = null)
	{

	}

	public static function set($key, $value, $user = null)
	{

	}

	public static function isset($key, $user = null)
	{

	}

	public static function unset($key, $user = null)
	{

	}
}
