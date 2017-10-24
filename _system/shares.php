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
	// returns one of ACCESS_NONE, ACCESS_READ_ONLY, ACCESS_WRITE
	public static function getUserAccessLevel($share, $user_id = null)
	{
		if (is_null($user_id)) {
			$user_id = Auth::getCurrentUserId();
		}
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			$query_result = Database::query('SELECT
						"shares_in_groups"."writable" AS "writable"
					FROM
						"users",
						"users_in_groups",
						"groups",
						"shares_in_groups",
						"shares"
					WHERE
						"users"."id" = ? AND
						"shares"."id" = ? AND
						"users_in_groups"."user_id" = "users"."id" AND
						"users_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."share_id" = "shares"."id" AND
						"groups"."enabled" != 0 AND
						"shares"."enabled" != 0;', [$user_id, $share]);
			if (isset($query_result[0])) {
				if ($query_result[0]['writable'] != 0) {
					return self::ACCESS_READ_WRITE;
				} else {
					return self::ACCESS_READ_ONLY;
				}
			} else {
				return self::ACCESS_NONE;
			}
		} else {
			return false;
		}
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
	public static function getAllAccessible($user_id = null)
	{
		if (is_null($user_id)) {
			$user_id = Auth::getCurrentUserId();
		}
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			$query_result = Database::query('SELECT
						"shares"."id" AS "id"
					FROM
						"users",
						"users_in_groups",
						"groups",
						"shares_in_groups",
						"shares"
					WHERE
						"users"."id" = ? AND
						"users_in_groups"."user_id" = "users"."id" AND
						"users_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."share_id" = "shares"."id" AND
						"groups"."enabled" != 0 AND
						"shares"."enabled" != 0;', [$user_id]);
			$shares = [];
			foreach ($query_result as $record) {
				$shares[] = $record['id'];
			}
			return $shares;
		} else {
			return false;
		}
	}

	public static function getAll($enabled_only = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "id" FROM "shares" WHERE "enabled" != 0;');
			} else {
				$query_result = Database::query('SELECT "id" FROM "shares";');
			}
			$shares = [];
			foreach ($query_result as $record) {
				$shares[] = $record['id'];
			}
			return $shares;
		} else {
			return false;
		}
	}
}
