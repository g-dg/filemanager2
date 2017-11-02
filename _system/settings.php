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
		$query_result = Database::query('SELECT "value" FROM "global_settings" WHERE "key" = ?;', [$key]);
		if (isset($query_result[0])) {
			return $query_result[0][0];
		} else {
			return $default;
		}
	}

	public static function set($key, $value, $force = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $force) {
			Database::query('INSERT INTO "global_settings" ("key", "value") VALUES (?, ?);', [$key, (string)$value]);
		}
	}

	public static function isset($key, $force = false)
	{
		return Database::query('SELECT COUNT() from "global_settings" WHERE "key" = ?;', [$key])[0][0] > 0;
	}

	public static function unset($key, $force = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $force) {
			Database::query('DELETE FROM "global_settings" WHERE "key" = ?;', [$key]);
		}
	}

	public static function getAll($force = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $force) {
			$keys = [];
			$query_result = Database::query('SELECT "key" FROM "global_settings";');
			foreach ($query_result as $record) {
				$keys[] = $record[0];
			}
			return $keys;
		}
		return null;
	}
}

class UserSettings
{
	protected static $settings_cache = [];

	public static function get($key, $default = null, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}

		if (Auth::getCurrentUserType() === Auth::USER_TYPE_GUEST) {
			$settings_cache = Session::get('_settings.guest_cache', []);
			if (isset($settings_cache[$key])) {
				return $settings_cache[$key];
			} else {
				return $default;
			}
		}
		
		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType === Auth::USER_TYPE_ADMIN || $force) {
			$query_result = Database::query('SELECT "value" FROM "user_settings" WHERE "key" = ? AND "user_id" = ?;', [$key, $user]);
			if (isset($query_result[0])) {
				return $query_result[0][0];
			} else {
				return $default;
			}
		}
	}

	public static function set($key, $value, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}

		if (Auth::getCurrentUserType() === Auth::USER_TYPE_GUEST) {
			$settings_cache = Session::get('_settings.guest_cache', []);
			$settings_cache[$key] = $value;
			Session::set('_settings.guest_cache', $settings_cache);
			return;
		}

		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType === Auth::USER_TYPE_ADMIN || $force) {
			Database::query('INSERT INTO "user_settings" ("key", "user_id", "value") VALUES (?, ?, ?);', [$key, $user, (string)$value]);
		}
	}

	public static function isset($key, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}

		if (Auth::getCurrentUserType() === Auth::USER_TYPE_GUEST) {
			$settings_cache = Session::get('_settings.guest_cache', []);
			return isset($settings_cache[$key]);
		}

		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType === Auth::USER_TYPE_ADMIN || $force) {
			return Database::query('SELECT COUNT() from "user_settings" WHERE "key" = ? AND "user_id" = ?;', [$key, $user])[0][0] > 0;
		}
	}

	public static function unset($key, $user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}

		if (Auth::getCurrentUserType() === Auth::USER_TYPE_GUEST) {
			$settings_cache = Session::get('_settings.guest_cache', []);
			unset($settings_cache[$key]);
			Session::set('_settings.guest_cache', $settings_cache);
		}

		if ($user === Auth::getCurrentUserId() || Auth::getCurrentUserType === Auth::USER_TYPE_ADMIN || $force) {
			Database::query('DELETE FROM "user_settings" WHERE "key" = ? AND "user_id" = ?;', [$key, $user]);
		}
	}

	public static function getAll($user = null, $force = false)
	{
		if (is_null($user)) {
			$user = Auth::getCurrentUserId();
		}

		if (Auth::getCurrentUserType() === Auth::USER_TYPE_GUEST) {
			$settings_cache = Session::get('_settings.guest_cache', []);
			$keys = [];
			foreach ($settings_cache as $key => $value) {
				$keys[] = $key;
			}
			return $keys;
		}

		if ($user = Auth::getCurrentUserId() || Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $force) {
			$keys = [];
			$query_result = Database::query('SELECT "key" FROM "user_settings" WHERE "user_id" = ?;', [$user]);
			foreach ($query_result as $record) {
				$keys[] = $record[0];
			}
			return $keys;
		}
		return null;
	}
}
