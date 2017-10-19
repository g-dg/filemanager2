<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Auth
{
	const USER_TYPE_ADMIN = 2;
	const USER_TYPE_STANDARD = 1;
	const USER_TYPE_GUEST = 0;

	const ERROR_NOT_LOGGED_IN = 1;
	const ERROR_DOESNT_EXIST = 2;
	const ERROR_INCORRECT_PASSWORD = 3;
	const ERROR_DISABLED = 4;

	protected static $user_id = null;
	protected static $user_name = null;
	protected static $user_type = 0;
	protected static $user_comment = '';

	// pass $username and $password to authenticate a new user
	public static function authenticate($username = null, $password = null)
	{
		if (is_null(self::$user_id)) {
			if (!is_null($username) && !is_null($password)) {
				// log in
				$user_record = Database::query('SELECT * FROM "users" WHERE "name" = ?;', [$username]);
				if (isset($user_record[0])) {
					$user_record = $user_record[0];
					// user exists
					if (password_verify($password, $user_record['password'])){
						// correct password
						if ($user_record['enabled'] == 1) {
							// user is enabled
							// authenticated
							self::$user_id = (int)$user_record['id'];
							self::$user_name = $user_record['name'];
							self::$user_type = (int)$user_record['type'];
							self::$user_comment = $user_record['comment'];
							return true;
						} else {
							return self::ERROR_DISABLED;
						}
					} else {
						return self::ERROR_INCORRECT_PASSWORD;
					}
				} else {
					return self::ERROR_DOESNT_EXIST;
				}
			} else {
				// get from session
				$user_id = Session::get('auth_user_id');
				if (!is_null($user_id)) {
					$user_record = Database::query('SELECT * FROM "users" WHERE "id" = ?;', [$user_id]);
					if (isset($user_record[0])) {
						$user_record = $user_record[0];
						// user exists
						if (password_verify($password, $user_record['password'])){
							// correct password
							if ($user_record['enabled'] == 1) {
								// user is enabled
								// authenticated
								self::$user_id = (int)$user_record['id'];
								self::$user_name = $user_record['name'];
								self::$user_type = (int)$user_record['type'];
								self::$user_comment = $user_record['comment'];
								return true;
							} else {
								return self::ERROR_DISABLED;
							}
						} else {
							return self::ERROR_INCORRECT_PASSWORD;
						}
					} else {
						return self::ERROR_DOESNT_EXIST;
					}
				} else {
					return self::ERROR_NOT_LOGGED_IN;
				}
			}
		} else {
			return true;
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
