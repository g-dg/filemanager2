<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Authenticate
{
	protected static $user_id = null;

	// pass $username and $password to authenticate a new user
	public static function authenticate($username = null, $password = null)
	{
		if (is_null(self::$user_id) && !is_null($username) && !is_null($password)) {
			Session::start();
			$user_record = Database::query('SELECT * FROM "users" WHERE "name" = ?;', $username);
			if (isset($user_record[0])) {
				$user_record = $user_record[0];
				// user exists
				if (password_verify($password, $user_record['password'])){
					// correct password
					// authenticated
					self::$user_id = $user_record['id'];
				}
			}
		}
	}

	public static function getCurrentUserId()
	{
		self::authenticate();
		return self::$user_id;
	}

	public static function logout($keep_session = true)
	{
		self::$user_id = null;
		Session::unsetSession(! $keep_session);
	}
}
