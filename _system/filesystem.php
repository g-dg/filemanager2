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
		$source = self::mapSharePathToFilesystemPath($source);
		$dest = self::mapSharePathToFilesystemPath($dest);
		if (!is_null($source) && !is_null($dest)) {
			return @copy($source, $dest);
		}
		return false;
	}
	public static function file_exists($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @file_exists($filename);
		}
		return false;
	}
	public static function file_get_contents($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @file_get_contents($filename);
		}
		return false;
	}
	public static function file_put_contents($filename, $data)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @file_put_contents($filename, $data);
		}
		return false;
	}
	public static function fileatime($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @fileatime($filename);
		}
		return false;
	}
	public static function filemtime($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @filemtime($filename);
		}
		return false;
	}
	public static function filesize($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @filesize($filename);
		}
		return false;
	}
	public static function filetype($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @filetype($filename);
		}
		return false;
	}
	// The built-in php file functions can be used on the returned handle.
	// Does not check user's permissions
	public static function fopen($filename, $mode)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @fopen($filename, $mode);
		}
		return false;
	}
	public static function is_dir($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_dir($filename);
		}
		return false;
	}
	public static function is_file($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_file($filename);
		}
		return false;
	}
	public static function is_readable($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_readable($filename);
		}
		return false;
	}
	public static function is_writable($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_writable($filename);
		}
		return false;
	}
	public static function mkdir($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @mkdir($filename);
		}
		return false;
	}
	public static function rename($oldname, $newname)
	{
		$oldname = self::mapSharePathToFilesystemPath($oldname);
		$newname = self::mapSharePathToFilesystemPath($newname);
		if (!is_null($oldname) && !is_null($newname)) {
			return @rename($oldname, $newname);
		}
		return false;
	}
	public static function rmdir($dirname)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @rmdir($dirname);
		}
		return false;
	}
	public static function touch($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @touch($filename);
		}
		return false;
	}
	public static function unlink($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @unlink($filename);
		}
		return false;
	}

	public static function scandir($path, $sorting_order = SCANDIR_SORT_ASCENDING)
	{
		$path = self::mapSharePathToFilesystemPath($path);
		if (!is_null($path)) {
			return @scandir($path, $sorting_order);
		}
		return false;
	}

	public static function processFileUpload($form_upload_element_name, $dest_filename)
	{
		$dest_filename = self::mapSharePathToFilesystemPath($dest_filename);
		if (!is_null($dest_filename)) {
			
		}
		return false;
	}

	public static function getMimeType($filename, $is_filesystem_path = false)
	{
		// check the extension
		
		if (isset(pathinfo($filename)['extension'])) {
			$fh = fopen('_res/mime.types', 'r');
			while (($line = fgets($fh)) !== false) {
				if (strlen($line) > 0 &&
						substr($line, 0, 1) !== '#' &&
						preg_match_all('/([^\s]+)/', $line, $record) &&
						isset($record[1]) &&
						($count = count($record[1])) > 1) {
					for ($i = 1; $i < $count; $i++) {
						if ($record[1][$i] == pathinfo($filename)['extension']) {
							return $record[1][0];
						}
					}
				}
			}
			fclose($fh);
		}

		if (!$is_filesystem_path) {
			$filename = self::mapSharePathToFilesystemPath($filename);
		}

		// try checking the content
		if (function_exists('finfo_open')) {
			if ($finfo = @finfo_open(FILEINFO_MIME_TYPE)) {
				if ($mimetype = @finfo_file($finfo, $filename)) {
					finfo_close($finfo);
					return $mimetype;
				}
			}
		}
		if (function_exists('mime_content_type')) {
			if ($unprocessed_content_type = @mime_content_type($filename)) {
				return preg_replace('~^(.+);.*$~', '$1', $unprocessed_content_type);
			}
		}

		// last ditch
		return 'application/octet-stream';
	}

	public static function cpRecursive($source, $destination)
	{
		$source = self::mapSharePathToFilesystemPath($source);
		$dest = self::mapSharePathToFilesystemPath($dest);
		if (!is_null($source) && !is_null($destination)) {

		}
		return false;
	}

	public static function rmRecursive($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			
		}
		return false;
	}


	// path doesn't have to exist (the share does)
	protected static function mapSharePathToFilesystemPath($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			$share_id = Shares::getId($share_path_array[0]);
			$access_level = Shares::getUserAccessLevel($share_id);
			if ($access_level === Shares::ACCESS_READ_ONLY || $access_level === Shares::ACCESS_READ_WRITE) {
				if (!is_null($share_path = rtrim(Shares::getPath($share_id), '/'))) {
					$path_array = $share_path_array;
					array_shift($path_array);
					$path = implode('/', $path_array);
					if ($path !== '') {
						return $share_path . '/' . $path;
					} else {
						return $share_path;
					}
				}
			}
		}
		return null;
	}

	protected static function sanitizeSharePath($path)
	{
		$raw_path_array = explode('/', $path);
		$path_array = [];
		foreach ($raw_path_array as $raw_path_part) {
			if (strlen($raw_path_part) > 0 && $raw_path_part !== '.') {
				if ($raw_path_part === '..') {
					if (count($path_array) > 0) {
						array_pop($path_array);
					}
				} else {
					array_push($path_array, $raw_path_part);
				}
			}
		}
		return '/' . implode('/', $path_array);
	}

	// returns null if none
	protected static function getShareName($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			return $share_path_array[0];
		} else {
			return null;
		}
	}

	// looks for the file or directory's name in the directory listing of dirname()
	protected static function fileActuallyExists($fs_path)
	{

	}
}
