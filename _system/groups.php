<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Groups {
	public static function addUser($group_id, $user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			try {
				Database::query('INSERT INTO "users_in_groups" ("user_id", "group_id") VALUES (?, ?);', [$user_id, $group_id]);
				Log::notice('User "' . Users::getName($user_id) . '" added to group "' . Groups::getName($group_id) . '" by "' . Auth::getCurrentUserName() . '"');
				return true;
			} catch (Exception $e){}
		}
		return false;
	}

	public static function removeUser($group_id, $user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('DELETE FROM "users_in_groups" WHERE "user_id" = ? AND "group_id" = ?;', [$user_id, $group_id]);
			Log::notice('User "' . Users::getName($user_id) . '" removed from group "' . Groups::getName($group_id) . '" by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function addShare($group_id, $share_id, $writable = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($writable) {
				$writable_int = 1;
			} else {
				$writable_int = 0;
			}
			try {
				Database::query('INSERT INTO "shares_in_groups" ("share_id", "group_id", "writable") VALUES (?, ?, ?);', [$share_id, $group_id, $writable_int]);
				Log::notice('Share "' . Shares::getName($share_id) . '" added to group "' . Groups::getName($group_id) . '" by "' . Auth::getCurrentUserName() . '"');
				return false;
			} catch (Exception $e){}
			return true;
		}
		return false;
	}

	public static function removeShare($group_id, $share_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('DELETE FROM "shares_in_groups" WHERE "share_id" = ? AND "group_id" = ?;', [$share_id, $group_id]);
			Log::notice('Share "' . Shares::getName($share_id) . '" removed from group "' . Groups::getName($group_id) . '" by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function getShareWritable($group_id, $share_id)
	{
		$query_result = Database::query('SELECT "writable" FROM "shares_in_groups" WHERE "share_id" = ? AND "group_id" = ?;', [$share_id, $group_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['writable'] != 0;
		}
		return null;
	}

	public static function setShareWritable($group_id, $share_id, $writable)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($writable) {
				$writable_int = 1;
			} else {
				$writable_int = 0;
			}
			Database::query('UPDATE "shares_in_groups" SET "writable" = ? WHERE "group_id" = ? AND "share_id" = ?;', [$writable_int, $group_id, $share_id]);
			if ($writable) {
				Log::notice('Write permission granted to group "' . Groups::getName($group_id) . '" for share "' . Shares::getName($share_id) . '" by "' . Auth::getCurrentUserName() . '"');
			} else {
				Log::notice('Write permission revoked from group "' . Groups::getName($group_id) . '" for share "' . Shares::getName($share_id) . '" by "' . Auth::getCurrentUserName() . '"');
			}
			return true;
		}
		return false;
	}

	public static function userInGroup($group_id, $user_id = null)
	{
		if (is_null($user_id)) {
			$user_id = Auth::getCurrentUserId();
		}
		if ($user_id === Auth::getCurrentUserId() || Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			return Database::query('SELECT COUNT() FROM "users_in_groups" WHERE "group_id" = ? AND "user_id" = ?;', [$group_id, $user_id])[0][0] != 0;
		}
		return null;
	}

	public static function shareInGroup($group_id, $share_id)
	{
		return Database::query('SELECT COUNT() FROM "users_in_groups" WHERE "group_id" = ? AND "share_id" = ?;', [$group_id, $share_id])[0][0] != 0;
	}

	public static function create($name, $enabled = true, $comment = '')
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if (!$enabled) {
				$enabled_int = 0;
			} else {
				$enabled_int = 1;
			}
			try {
				Database::query('INSERT INTO "groups" ("name", "enabled", "comment") VALUES (?, ?, ?);', [$name, $enabled_int, $comment]);
				Log::notice('Group "' . $name . '" created by "' . Auth::getCurrentUserName() . '"');
				return true;
			} catch (\Exception $e){}
		}
		return false;
	}

	public static function delete($group_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$name = Groups::getName($group_id);
			Database::query('DELETE FROM "groups" WHERE "id" = ?;', [$group_id]);
			Log::notice('Group "' . $name . '" deleted by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function getId($name)
	{
		$query_result = Database::query('SELECT "id" FROM "groups" WHERE "name" = ?;', [$name]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['id'];
		}
		return null;
	}

	public static function getName($group_id)
	{
		$query_result = Database::query('SELECT "name" FROM "groups" WHERE "id" = ?;', [$group_id]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['name'];
		}
		return null;
	}

	public static function setName($group_id, $new_name)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$old_name = Groups::getName($group_id);
			Database::query('UPDATE "groups" SET "name" = ? WHERE "id" = ?;', [$new_name, $group_id]);
			Log::notice('Group "' . $old_name . '" renamed to "' . $new_name . '" by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}
	
	public static function getEnabled($group_id)
	{
		$query_result = Database::query('SELECT "enabled" FROM "groups" WHERE "id" = ?;', [$group_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['enabled'] != 0;
		}
		return null;
	}
	
	public static function setEnabled($group_id, $enabled)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if (!$enabled) {
				$enabled_int = 0;
			} else {
				$enabled_int = 1;
			}
			Database::query('UPDATE "groups" SET "enabled" = ? WHERE "id" = ?;', [$enabled_int, $group_id]);
			Log::notice('Group "' . Groups::getName($user_id) . '" ' . ($enabled?'enabled':'disabled') . ' by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function getComment($group_id)
	{
		$query_result = Database::query('SELECT "comment" FROM "groups" WHERE "id" = ?;', [$group_id]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['comment'];
		}
		return null;
	}

	public static function setComment($group_id, $comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "groups" SET "comment" = ? WHERE "id" = ?;', [$comment, $group_id]);
			Log::notice('Comment for group "' . Users::getName($user_id) . '" changed by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function getUsersInGroup($group_id, $enabled_only = false)
	{
		/*
			SELECT
				"users"."id"
			FROM
				"users",
				"users_in_groups"
			WHERE
				"users_in_groups"."group_id" = ? AND
				"users_in_groups"."user_id" = "users"."id" AND
				"users"."enabled" != 0;
		*/
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "users"."id" FROM "users", "users_in_groups" WHERE "users_in_groups"."group_id" = ? AND "users_in_groups"."user_id" = "users"."id" AND "users"."enabled" != 0;', [$group_id]);
			} else {
				$query_result = Database::query('SELECT "user_id" FROM "users_in_groups" WHERE "group_id" = ?;', [$group_id]);
			}
			$users = [];
			foreach ($query_result as $record) {
				$users[] = $record['id'];
			}
			return $users;
		}
		return false;
	}

	public static function getSharesInGroup($group_id, $enabled_only = false)
	{
		/*
			SELECT
				"shares"."id"
			FROM
				"shares",
				"shares_in_groups"
			WHERE
				"shares_in_groups"."group_id" = ? AND
				"shares_in_groups"."share_id" = "shares"."id" AND
				"shares"."enabled" != 0;
		*/
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "shares"."id" FROM "shares", "shares_in_groups" WHERE "shares_in_groups"."group_id" = ? AND "shares_in_groups"."share_id" = "shares"."id" AND "shares"."enabled" != 0;', [$group_id]);
			} else {
				$query_result = Database::query('SELECT "share_id" FROM "shares_in_groups" WHERE "group_id" = ?;', [$group_id]);
			}
			$groups = [];
			foreach ($query_result as $record) {
				$groups[] = $record['id'];
			}
			return $groups;
		}
		return false;
	}

	public static function getAll($enabled_only = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "id" FROM "groups" WHERE "enabled" != 0;');
			} else {
				$query_result = Database::query('SELECT "id" FROM "groups";');
			}
			$groups = [];
			foreach ($query_result as $record) {
				$groups[] = $record['id'];
			}
			return $groups;
		}
		return false;
	}
}
