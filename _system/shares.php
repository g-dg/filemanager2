<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Shares {
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
				Log::notice('Share "' . $name . '" created by "' . Auth::getCurrentUserName() . '"');
				return true;
			} catch (\Exception $e){}
		}
		return false;
	}
	public static function delete($share_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$name = Shares::getName($share_id);
			Database::query('DELETE FROM "shares" WHERE "id" = ?;', [$share_id]);
			Log::notice('Share "' . $name . '" deleted by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function setName($share_id, $new_name)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$old_name = Shares::getName($share_id);
			Database::query('UPDATE "shares" SET "name" = ? WHERE "id" = ?;', [$new_name, $share_id]);
			Log::notice('Share "' . $old_name . '" renamed to "' . $new_name . '" by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}
	public static function setPath($share_id, $new_path)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "shares" SET "path" = ? WHERE "id" = ?;', [$new_path, $share_id]);
			Log::notice('Path for share "' . Shares::getName($share_id) . '" changed by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}
	public static function setEnabled($share_id, $enabled)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled) {
				$enabled_int = 1;
			} else {
				$enabled_int = 0;
			}
			Database::query('UPDATE "shares" SET "enabled" = ? WHERE "id" = ?;', [$enabled_int, $share_id]);
			Log::notice('Share "' . Shares::getName($share_id) . '" ' . ($enabled?'enabled':'disabled') . ' by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}
	public static function setComment($share_id, $new_comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "shares" SET "comment" = ? WHERE "id" = ?;', [$new_comment, $share_id]);
			Log::notice('Comment for share "' . Shares::getName($share_id) . '" changed by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function getId($name)
	{
		$query_result = Database::query('SELECT "id" FROM "shares" WHERE "name" = ?;', [$name]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['id'];
		}
		return null;
	}
	public static function getName($share_id)
	{
		$query_result = Database::query('SELECT "name" FROM "shares" WHERE "id" = ?;', [$share_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['name'];
		}
		return null;
	}
	public static function getPath($share_id)
	{
		$query_result = Database::query('SELECT "path" FROM "shares" WHERE "id" = ?;', [$share_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['path'];
		}
		return null;
	}
	public static function getEnabled($share_id)
	{
		$query_result = Database::query('SELECT "enabled" FROM "shares" WHERE "id" = ?;', [$share_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['enabled'] != 0;
		}
		return null;
	}
	public static function getComment($share_id)
	{
		$query_result = Database::query('SELECT "comment" FROM "shares" WHERE "id" = ?;', [$share_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['comment'];
		}
		return null;
	}

	public static function canRead($share_id, $user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			$query_result = Database::query('SELECT
						COUNT()
					FROM
						"users_in_groups",
						"groups",
						"shares_in_groups",
						"shares"
					WHERE
						"users_in_groups"."user_id" = ? AND
						"shares"."id" = ? AND
						"users_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."share_id" = "shares"."id" AND
						"groups"."enabled" != 0 AND
						"shares"."enabled" != 0;', [$user_id, $share_id]);
			return $query_result[0][0] > 0;
		}
		return null;
	}
	public static function canWrite($share_id, $user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			$query_result = Database::query('SELECT
						COUNT()
					FROM
						"users_in_groups",
						"groups",
						"shares_in_groups",
						"shares"
					WHERE
						"users_in_groups"."id" = ? AND
						"shares"."id" = ? AND
						"users_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."group_id" = "groups"."id" AND
						"shares_in_groups"."share_id" = "shares"."id" AND
						"shares_in_groups"."writable" != 0 AND
						"groups"."enabled" != 0 AND
						"shares"."enabled" != 0;', [$user_id, $share_id]);
			return $query_result[0][0] > 0;
		}
		return null;
	}

	public static function getGroups($share_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$query_result = Database::query('SELECT "group_id" FROM "shares_in_groups" WHERE "share_id" = ?;', [$share_id]);
			$group_ids = [];
			foreach ($query_result as $record) {
				$group_ids[] = $record[0];
			}
			return $group_ids;
		}
		return null;
	}

	// if $user is null, use the current user id
	public static function getAllAccessible($user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			/*
				SELECT DISTINCT
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
					"shares"."enabled" != 0;
			*/
			$query_result = Database::query('SELECT DISTINCT
						"shares"."id" AS "id"
					FROM
						"users_in_groups",
						"groups",
						"shares_in_groups",
						"shares"
					WHERE
						"users_in_groups"."user_id" = ? AND
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
		}
		return false;
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
		}
		return false;
	}
}
