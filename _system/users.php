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
				Log::notice('User "' . $username . '" created by "' . Auth::getCurrentUserName() . '"');
				return true;
			} catch (\Exception $e){}
		}
		return false;
	}
	public static function delete($user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$username = Users::getName($user_id);
			Database::query('DELETE FROM "users" WHERE "id" = ?;', [$user_id]);
			Log::notice('User "' . $username . '" deleted by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function setName($user_id, $new_name)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			$old_name = Users::getName($user_id);
			Database::query('UPDATE "users" SET "name" = ? WHERE "id" = ?;', [$new_name, $user_id]);
			Log::notice('User "' . $old_name . '" renamed to "' . $new_name . '" by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}
	public static function setPassword($user_id, $password)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN || $user_id === Auth::getCurrentUserId()) {
			Database::query('UPDATE "users" SET "password" = ? WHERE "id" = ?;', [password_hash($password, PASSWORD_DEFAULT), $user_id], false);
			Log::notice('Password for "' . Users::getName($user_id) . '" changed by "' . Auth::getCurrentUserId() . '"');
			return true;
		}
		return false;
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
			Log::notice('User "' . Users::getName($user_id) . '" ' . ($enabled?'enabled':'disabled') . ' by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}
	public static function setType($user_id, $type)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($type !== self::USER_TYPE_ADMIN && $type !== self::USER_TYPE_STANDARD && $type !== self::USER_TYPE_GUEST) {
				return false;
			}
			Database::query('UPDATE "users" SET "type" = ? WHERE "id" = ?;', [$type, $user_id]);
			switch ($type) {
				case self::USER_TYPE_ADMIN:
					Log::notice('User "' . Users::getName($user_id) . '" changed to administrator by "' . Auth::getCurrentUserName() . '"');
					break;
				case self::USER_TYPE_STANDARD:
					Log::notice('User "' . Users::getName($user_id) . '" changed to standard user by "' . Auth::getCurrentUserName() . '"');
					break;
				case self::USER_TYPE_GUEST:
					Log::notice('User "' . Users::getName($user_id) . '" changed to guest by "' . Auth::getCurrentUserName() . '"');
					break;
			}
			return true;
		}
		return false;
	}
	public static function setComment($user_id, $comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "users" SET "comment" = ? WHERE "id" = ?;', [$comment, $user_id]);
			Log::notice('Comment for "' . Users::getName($user_id) . '" changed by "' . Auth::getCurrentUserName() . '"');
			return true;
		}
		return false;
	}

	public static function getId($username)
	{
		$query_result = Database::query('SELECT "id" FROM "users" WHERE "name" = ?;', [$username]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['id'];
		}
		return null;
	}
	public static function getName($user_id)
	{
		$query_result = Database::query('SELECT "name" FROM "users" WHERE "id" = ?;', [$user_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['name'];
		}
		return null;
	}
	public static function getEnabled($user_id)
	{
		$query_result = Database::query('SELECT "enabled" FROM "users" WHERE "id" = ?;', [$user_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['enabled'] != 0;
		}
		return null;
	}
	public static function getType($user_id)
	{
		$query_result = Database::query('SELECT "type" FROM "users" WHERE "id" = ?;', [$user_id]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['type'];
		}
		return null;
	}
	public static function getComment($user_id)
	{
		$query_result = Database::query('SELECT "comment" FROM "users" WHERE "id" = ?;', [$user_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['comment'];
		}
		return null;
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
				$users[] = $record['id'];
			}
			return $users;
		}
		return false;
	}
}
