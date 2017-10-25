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

			return true;
		} else {
			return false;
		}
	}

	public static function removeUser($group_id, $user_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function addShare($group_id, $share_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function removeShare($group_id, $share_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function userInGroup($group_id, $user = null)
	{

	}

	public static function shareInGroup($group_id, $share)
	{

	}

	public static function create($name, $enabled = true, $comment = '')
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled) {
				$enabled_int = 1;
			} else {
				$enabled_int = 0;
			}
			try {
				Database::query('INSERT INTO "groups" ("name", "enabled", "comment") VALUES "(?, ?, ?);', [$name, $enabled_int, $comment]);
				return true;
			} catch (\Exception $e) {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function delete($group_id)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('DELETE FROM "groups" WHERE "id" = ?;', [$group_id]);
			return true;
		} else {
			return false;
		}
	}

	public static function getId($name)
	{
		$query_result = Database::query('SELECT "id" FROM "groups" WHERE "name" = ?;', [$name]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['id'];
		} else {
			return null;
		}
	}

	public static function getName($group_id)
	{
		$query_result = Database::query('SELECT "name" FROM "groups" WHERE "id" = ?;', [$group_id]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['name'];
		} else {
			return null;
		}
	}

	public static function setName($group_id, $new_name)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "groups" SET "name" = ? WHERE "id" = ?;', [$new_name, $group_id]);
			return true;
		} else {
			return false;
		}
	}
	
	public static function getEnabled($group_id)
	{
		$query_result = Database::query('SELECT "enabled" FROM "groups" WHERE "id" = ?;', [$group_id]);
		if (isset($query_result[0])) {
			return $query_result[0]['enabled'] != 0;
		} else {
			return null;
		}
	}
	
	public static function setEnabled($group_id, $enabled)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled) {
				$enabled_int = 1;
			} else {
				$enabled_int = 0;
			}
			Database::query('UPDATE "groups" SET "enabled" = ? WHERE "id" = ?;', [$enabled_int, $group_id]);
			return true;
		} else {
			return false;
		}
	}

	public static function getComment($group_id)
	{
		$query_result = Database::query('SELECT "comment" FROM "groups" WHERE "id" = ?;', [$group_id]);
		if (isset($query_result[0])) {
			return (int)$query_result[0]['comment'];
		} else {
			return null;
		}
	}

	public static function setComment($group_id, $comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			Database::query('UPDATE "groups" SET "name" = ? WHERE "id" = ?;', [$new_name, $group_id]);
			return true;
		} else {
			return false;
		}
	}

	public static function getUsersInGroup($group_id, $enabled_only = false)
	{

	}

	public static function getSharesInGroup($group_id, $enabled_only = false)
	{

	}

	public static function getAll($enabled_only = false)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {
			if ($enabled_only) {
				$query_result = Database::query('SELECT "id" FROM "groups" WHERE "enabled" != 0;');
			} else {
				$query_result = Database::query('SELECT "id" FROM "groups";');
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
