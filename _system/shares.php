<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Shares {
	const ACCESS_NONE = 0;
	const ACCESS_READ_ONLY = 1;
	const ACCESS_READ_WRITE = 2;

	// if $user_id is null, the current user id is used.
	// if user doesn't exist, ACCESS_NONE is returned.
	// returns one of ACCESS_NONE, ACCESS_READ_ONLY, ACCESS_WRITE
	public static function getUserAccessLevel($share, $user_id=null)
	{

	}

	// must be an administrator
	public static function create($name, $path, $enabled = true, $comment = '')
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled) {
				$enabled_int = 1;
			} else {
				$enabled_int = 0;
			}
			try {
				Database::query('INSERT INTO "shares" ("name", "path", "enabled", "comment") VALUES (?, ?, ?, ?);', [$name, $path, $enabled_int, $comment]);
				return true;
			} catch (\Exception $e) {
				return false;
			}
		} else {
			return false;
		}
	}
	public static function delete($share)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('DELETE FROM "shares" WHERE "id" = ?;', [$share]);
			return true;
		} else {
			return false;
		}
	}

	public static function setName($share, $new_name)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "users" SET "name" = ? WHERE "id" = ?;', [$new_name, $user_id]);
			return true;
		} else {
			return false;
		}
	}
	public static function setPath($share, $new_path)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "users" SET "path" = ? WHERE "id" = ?;', [$new_path, $user_id]);
			return true;
		} else {
			return false;
		}
	}
	public static function setEnabled($share, $enabled)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled) {
				$enabled_int = 1;
			} else {
				$enabled_int = 0;
			}
			Database::query('UPDATE "users" SET "enabled" = ? WHERE "id" = ?;', [$enabled_int, $user_id]);
			return true;
		} else {
			return false;
		}
	}
	public static function setComment($share, $new_comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "users" SET "comment" = ? WHERE "id" = ?;', [$new_comment, $user_id]);
			return true;
		} else {
			return false;
		}
	}

	public static function getID($name)
	{
		$query_result = Database::query('SELECT "id" FROM "shares" WHERE "name" = ?;', [$name]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['id'];
		} else {
			return null;
		}
	}
	public static function getName($share)
	{
		$query_result = Database::query('SELECT "name" FROM "shares" WHERE "id" = ?;', [$share]);
		if (isset($query_result[0])) {
			return $query_result[0]['name'];
		} else {
			return null;
		}
	}
	public static function getPath($share)
	{
		$query_result = Database::query('SELECT "path" FROM "shares" WHERE "id" = ?;', [$share]);
		if (isset($query_result[0])) {
			return $query_result[0]['path'];
		} else {
			return null;
		}
	}
	public static function getEnabled($share)
	{
		$query_result = Database::query('SELECT "enabled" FROM "shares" WHERE "id" = ?;', [$share]);
		if (isset($query_result[0])) {
			return $query_result[0]['enabled'] != 0;
		} else {
			return null;
		}
	}
	public static function getComment($share)
	{
		$query_result = Database::query('SELECT "comment" FROM "shares" WHERE "id" = ?;', [$share]);
		if (isset($query_result[0])) {
			return $query_result[0]['comment'];
		} else {
			return null;
		}
	}

	// if $user is null, use the current user id
	public static function getAllAccessible($user = null)
	{

	}

	public static function getAll($enabled_only = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "id" FROM "shares" WHERE "enabled" != 0;');
			} else {
				$query_result = Database::query('SELECT "id" FROM "shares";');
			}
			$users = [];
			foreach ($query_result as $record) {
				$users[] = $record['id'];
			}
			return $users;
		} else {
			return false;
		}
	}
}
