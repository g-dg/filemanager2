<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Filesystem
{
	// the paths to the public methods are share-relative paths ("/share/directory/file")

	public static function copy($source, $dest)
	{

	}
	public static function file_exists($filename)
	{

	}
	public static function file_get_contents($filename)
	{

	}
	public static function file_put_contents($filename, $data, $flags = 0)
	{

	}
	public static function fileatime($filename)
	{

	}
	public static function filemtime($filename)
	{

	}
	public static function filesize($filename)
	{

	}
	public static function filetype($filename)
	{

	}
	// The built-in php file functions can be used on the returned handle.
	// Does not check user's permissions
	public static function fopen($filename, $mode)
	{

	}
	public static function is_dir($filename)
	{

	}
	public static function is_file($filename)
	{

	}
	public static function is_readable($filename)
	{

	}
	public static function is_writable($filename)
	{

	}
	public static function mkdir($filename)
	{

	}
	public static function rename($oldname, $newname)
	{

	}
	public static function rmdir($dirname)
	{

	}
	public static function touch($filename)
	{

	}
	public static function unlink($filename)
	{

	}

	// The built-in php directory functions can be used on the returned handle.
	// Does not check user's permissions
	public static function opendir($path)
	{

	}
	public static function scandir($path, $sorting_order = SCANDIR_SORT_ASCENDING)
	{

	}

	public static function processFileUpload($form_upload_element_name, $dest_filename)
	{

	}

	// uses filename extension
	public static function getMimeType($filename)
	{

	}

	public static function cpRecursive($source, $destination)
	{

	}

	public static function rmRecursive($filename)
	{

	}


	// path doesn't have to exist
	protected static function mapSharePathToFilesystemPath($share_path)
	{

	}

	protected static function sanitizeSharePath($path)
	{

	}

	protected static function getShareName($share_path)
	{

	}
	// looks for the file or directory's name in the directory listing of dirname()
	protected static function fileActuallyExists($fs_path)
	{

	}
}
