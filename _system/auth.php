<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Auth
{
	const USER_TYPE_ADMIN = 2;
	const USER_TYPE_STANDARD = 1;
	const USER_TYPE_GUEST = 0;

	protected static $user_id = null;
	protected static $user_name = null;
	protected static $user_type = 0;
	protected static $user_comment = '';

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
					if ($user_record['enabled'] == 1) {
						// user is enabled
						// authenticated
						self::$user_id = $user_record['id'];
						self::$user_name = $user_record['name'];
						self::$user_type = (int)$user_record['type'];
						self::$user_comment = $user_record['comment'];
					}
				}
			}
		}
	}

	public static function getCurrentUserId()
	{
		self::authenticate();
		return self::$user_id;
	}

	public static function getCurrentUserName()
	{
		self::authenticate();
		return self::$user_name;
	}

	public static function getCurrentUserType()
	{
		self::authenticate();
		return self::$user_type;
	}

	public static function getCurrentUserComment()
	{
		self::authenticate();
		return self::$user_comment;
	}

	public static function logout($keep_session = true)
	{
		self::$user_id = null;
		Session::unsetSession(! $keep_session);
	}
}
