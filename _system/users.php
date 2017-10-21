<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Users
{
	const USER_TYPE_ADMIN = 2;
	const USER_TYPE_STANDARD = 1;
	const USER_TYPE_GUEST = 0;


	public static function create($username, $password, $type = self::USER_TYPE_STANDARD, $enabled = true, $comment = '')
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			if ($type !== self::USER_TYPE_ADMIN && $type !== self::USER_TYPE_STANDARD && $type !== self::USER_TYPE_GUEST) {
				return false;
			}
			if ($enabled) {
				$enabled_int = 1;
			} else {
				$enabled_int = 0;
			}

			try {
				Database::query('INSERT INTO "users" ("name", "password", "enabled", "type", "comment") VALUES (?, ?, ?, ?, ?);', [$username, $password_hash, $enabled_int, $type, $comment]);
				return true;
			} catch (\Exception $e) {
				return false;
			}
		} else {
			return false;
		}
	}
	public static function delete($user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('DELETE FROM "users" WHERE "id" = ?;', [$user_id]);
			return true;
		} else {
			return false;
		}
	}

	public static function setName($user_id, $new_name)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "users" SET "name" = ? WHERE "id" = ?;', [$new_name, $user_id]);
			return true;
		} else {
			return false;
		}
	}
	public static function setPassword($user_id, $password)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			Database::query('UPDATE "users" SET "password" = ? WHERE "id" = ?;', [password_hash($password, PASSWORD_DEFAULT), $user_id]);
			return true;
		} else {
			return false;
		}
	}
	public static function setEnabled($user_id, $enabled)
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
	public static function setType($user_id, $type)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($type !== self::USER_TYPE_ADMIN && $type !== self::USER_TYPE_STANDARD && $type !== self::USER_TYPE_GUEST) {
				return false;
			}
			Database::query('UPDATE "users" SET "type" = ? WHERE "id" = ?;', [$type, $user_id]);
			return true;
		} else {
			return false;
		}
	}
	public static function setComment($user_id, $comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "users" SET "comment" = ? WHERE "id" = ?;', [$enabled_int, $comment]);
			return true;
		} else {
			return false;
		}
	}

	public static function getId($username)
	{
		$query_result = Database::query('SELECT "id" FROM "users" WHERE "name" = ?;', [$username]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['id'];
		} else {
			return null;
		}
	}
	public static function getName($user_id)
	{
		$query_result = Database::query('SELECT "name" FROM "users" WHERE "name" = ?;', [$username]);
		if (isset($query_result[0])) {
			return $query_result[0]['name'];
		} else {
			return null;
		}
	}
	public static function getEnabled($user_id)
	{
		$query_result = Database::query('SELECT "enabled" FROM "users" WHERE "name" = ?;', [$username]);
		if (isset($query_result[0])) {
			return $query_result[0]['enabled'] != 0;
		} else {
			return null;
		}
	}
	public static function getType($user_id)
	{
		$query_result = Database::query('SELECT "type" FROM "users" WHERE "name" = ?;', [$username]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['type'];
		} else {
			return null;
		}
	}
	public static function getComment($user_id)
	{
		$query_result = Database::query('SELECT "comment" FROM "users" WHERE "name" = ?;', [$username]);
		if (isset($query_result[0])) {
			return $query_result[0]['comment'];
		} else {
			return null;
		}
	}

	public static function getCurrentId()
	{
		return Auth::getCurrentUserId();
	}

	public static function getAll($enabled_only = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "id" FROM "users" WHERE "enabled" != 0;');
			} else {
				$query_result = Database::query('SELECT "id" FROM "users";');
			}
			$users = [];
			foreach ($query_result as $record) {
				array_push($users, $record['id']);
			}
			return $users;
		} else {
			return false;
		}
	}
}
