<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class GlobalSettings
{
	public static function get($key, $default)
	{

	}

	public static function set($key, $value)
	{

	}

	public static function isset($key)
	{

	}

	public static function unset($key)
	{

	}
}

class UserSettings
{
	public static function get($key, $default, $user = null)
	{

	}

	public static function set($key, $value, $user = null)
	{

	}

	public static function isset($key, $user = null)
	{

	}

	public static function unset($key, $user = null)
	{

	}
}
