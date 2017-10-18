<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Authenticate
{
	// pass $username and $password to authenticate a new user
	public static function authenticate($username = null, $password = null)
	{

	}

	public static function logout($keep_session = true)
	{

	}
}
