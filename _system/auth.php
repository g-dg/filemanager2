<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Authenticate
{
	protected static $authenticated = false;

	// pass $username and $password to authenticate a new user
	public static function authenticate($username = null, $password = null)
	{
		if (!self::$authenticated) {
			Session::start();

		}
	}

	public static function getCurrentUserId()
	{
		self::authenticate();
		return Session::get('__user_id', null);
	}

	public static function logout($keep_session = true)
	{
		Session::unsetSession(false);
	}
}
