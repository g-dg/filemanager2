<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Groups {
	const ACCESS_NONE = Shares::ACCESS_NONE;
	const ACCESS_READ_ONLY = Shares::ACCESS_READ_ONLY;
	const ACCESS_READ_WRITE = Shares::ACCESS_READ_WRITE;

	public static function addUser($group, $user)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function removeUser($group, $user)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function addShare($group, $share)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function removeShare($group, $share)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function userInGroup($group, $user = null)
	{

	}

	public static function shareInGroup($group, $share)
	{

	}

	public static function create($name, $enabled = true)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function getId($name)
	{

	}

	public static function setName($group, $new_name)
	{

	}

	public static function getName($group)
	{

	}

	public static function delete($group)
	{

	}
	
	public static function getEnabled($group)
	{

	}
	
	public static function setEnabled($group, $enabled)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function getComment($group)
	{

	}

	public static function setComment($group, $comment)
	{
		if (Auth::getCurrentUserType() === Auth::USER_TYPE_ADMIN) {

			return true;
		} else {
			return false;
		}
	}

	public static function getUsersInGroup($group, $enabled_only = false)
	{

	}

	public static function getSharesInGroup($group, $enabled_only = false)
	{

	}

	public static function getGroups($enabled_only = false)
	{

	}
}
