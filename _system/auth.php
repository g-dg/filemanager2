<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Auth
{
	const USER_TYPE_ADMIN = Users::USER_TYPE_ADMIN;
	const USER_TYPE_STANDARD = Users::USER_TYPE_STANDARD;
	const USER_TYPE_GUEST = Users::USER_TYPE_GUEST;

	const ERROR_NOT_LOGGED_IN = 1;
	const ERROR_DOESNT_EXIST = 2;
	const ERROR_INCORRECT_PASSWORD = 3;
	const ERROR_DISABLED = 4;

	const DEFAULT_LOGIN_PAGE = '/login';

	protected static $user_id = null;
	protected static $user_name = null;
	protected static $user_type = null;
	protected static $user_comment = null;

	// pass $username and $password to authenticate a new user
	// Note: if $redirect is false, this function does not ensure authentication.
	// Instead, it returns whether the authentication was successful
	public static function authenticate($redirect = true, $username = null, $password = null)
	{
		if (is_null(self::$user_id)) {
			if (!is_null($username) && !is_null($password)) {
				// log in
				// lock the database in case password needs rehashing
				Database::lock();
				$user_record = Database::query('SELECT * FROM "users" WHERE "name" = ?;', [$username]);
				if (isset($user_record[0])) {
					$user_record = $user_record[0];
					// user exists
					if (password_verify($password, $user_record['password'])){
						// correct password
						if (password_needs_rehash($user_record['password'], PASSWORD_DEFAULT)) {
							Database::query('UPDATE "users" SET "password" = ? WHERE "id" = ?;', [password_hash($password, PASSWORD_DEFAULT), $user_record['id']]);
						}
						if ($user_record['enabled'] != 0) {
							// user is enabled
							// authenticated
							self::$user_id = (int)$user_record['id'];
							self::$user_name = $user_record['name'];
							self::$user_type = (int)$user_record['type'];
							self::$user_comment = $user_record['comment'];
							$auth_status = true;
						} else {
							$auth_status = self::ERROR_DISABLED;
						}
					} else {
						$auth_status = self::ERROR_INCORRECT_PASSWORD;
					}
				} else {
					$auth_status = self::ERROR_DOESNT_EXIST;
				}
				Database::unlock();
			} else {
				// get from session
				$user_id = Session::get('_auth_user_id');
				if (!is_null($user_id)) {
					$user_record = Database::query('SELECT * FROM "users" WHERE "id" = ?;', [$user_id]);
					if (isset($user_record[0])) {
						$user_record = $user_record[0];
						// user exists
						if ($user_record['enabled'] != 0) {
							// user is enabled
							// authenticated
							self::$user_id = (int)$user_record['id'];
							self::$user_name = $user_record['name'];
							self::$user_type = (int)$user_record['type'];
							self::$user_comment = $user_record['comment'];
							$auth_status = true;
						} else {
							$auth_status = self::ERROR_DISABLED;
						}
					} else {
						$auth_status = self::ERROR_DOESNT_EXIST;
					}
				} else {
					$auth_status = self::ERROR_NOT_LOGGED_IN;
				}
			}
		} else {
			$auth_status = true;
		}

		Session::unset('_auth_status');
		if ($auth_status !== true) {
			if ($redirect) {
				Session::set('_login_target', $_SERVER['REQUEST_URI']);
				Session::set('_auth_status', $auth_status);
				Router::redirect(GlobalSettings::get('_auth_login_page', self::DEFAULT_LOGIN_PAGE));
				return false;
			}
		}
		return $auth_status;
	}

	public static function isAuthenticated()
	{
		return !is_null(self::$user_id);
	}

	public static function getCurrentUserId($authenticate = true)
	{
		if ($authenticate) {
			self::authenticate();
		}
		return self::$user_id;
	}

	public static function getCurrentUserName($authenticate = true)
	{
		if ($authenticate) {
			self::authenticate();
		}
		return self::$user_name;
	}

	public static function getCurrentUserType($authenticate = true)
	{
		if ($authenticate) {
			self::authenticate();
		}
		return self::$user_type;
	}

	public static function getCurrentUserComment($authenticate = true)
	{
		if ($authenticate) {
			self::authenticate();
		}
		return self::$user_comment;
	}

	public static function logout($keep_session = true)
	{
		self::$user_id = null;
		Session::unsetSession(! $keep_session);
	}
}
