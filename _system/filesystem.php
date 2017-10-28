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
			if (self::isPathWritable($dest)) {
				return @copy($source, $dest);
			}
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
			if (self::isPathWritable($filename)) {
				return @file_put_contents($filename, $data);
			}
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
	// Requires read permission on the share
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
			if (self::isPathWritable($filename)) {
				return @is_writable($filename);
			}
		}
		return false;
	}
	public static function mkdir($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename)) {
				return @mkdir($filename);
			}
		}
		return false;
	}
	public static function rename($oldname, $newname)
	{
		$oldname = self::mapSharePathToFilesystemPath($oldname);
		$newname = self::mapSharePathToFilesystemPath($newname);
		if (!is_null($oldname) && !is_null($newname)) {
			if (self::isPathWritable($oldname) && self::isPathWritable($newname)) {
				return @rename($oldname, $newname);
			}
		}
		return false;
	}
	public static function rmdir($dirname)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($dirname)) {
				return @rmdir($dirname);
			}
		}
		return false;
	}
	public static function touch($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename)) {
				return @touch($filename);
			}
		}
		return false;
	}
	public static function unlink($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename)) {
				return @unlink($filename);
			}
		}
		return false;
	}

	public static function scandir($path)
	{
		if (self::isPathToRoot($path)) {
			$share_ids = Shares::getAllAccessible();
			$share_names = [];
			foreach ($share_ids as $share_id) {
				$share_names[] = Shares::getname($share_id);
			}
			return $share_names;
		}

		$path = self::mapSharePathToFilesystemPath($path);
		if (!is_null($path)) {
			return @scandir($path);
		}

		return false;
	}
	public static function fileCount($path)
	{
		if (self::isPathToRoot($path)) {
			return count(Shares::getAllAccessible());
		}

		$path = self::mapSharePathToFilesystemPath($path);
		if (!is_null($path)) {
			if (is_dir($path) && ($dh = opendir($path))) {
				$count = 0;
				while (($entry = readdir($handle)) !== false) {
					if (substr($entry, 0, 1) !== '.') {
						$count++;
					}
				}
				closedir($handle);
				return $count;
			}
		}

		return false;
	}

	public static function processFileUpload($form_upload_element_name, $dest_filename)
	{
		$dest_filename = self::mapSharePathToFilesystemPath($dest_filename);
		if (!is_null($dest_filename)) {
			if (self::isPathWritable($dest_filename)) {
				if (move_uploaded_file($_FILES[$form_upload_element_name]['tmp_name'], $dest_filename)) {
					return true;
				}
			}
		}
		return false;
	}

	public static function getMimeType($filename, $is_filesystem_path = false)
	{
		if (!$is_filesystem_path) {
			$filename = self::mapSharePathToFilesystemPath($filename);
		}
		if (is_null($filename)) {
			return false;
		}

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


	protected static function isPathToRoot($path)
	{
		$path = self::sanitizeSharePath($path);
		$path_array = explode('/', trim($path, '/'));
		return !isset($path_array[1]);
	}

	// path doesn't have to exist (the share does)
	protected static function mapSharePathToFilesystemPath($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			$share_id = Shares::getId($share_path_array[0]);
			if (Shares::canRead($share_id)) {
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

	// looks for the file or directory's name in the directory listing of dirname()
	protected static function fileActuallyExists($fs_path)
	{

	}

	protected static function isPathReadable($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			return Shares::canRead(Shares::getId($share_path_array[0]));
		} else {
			return false;
		}
	}
	protected static function isPathWritable($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			return Shares::canWrite(Shares::getId($share_path_array[0]));
		} else {
			return false;
		}
	}
}
